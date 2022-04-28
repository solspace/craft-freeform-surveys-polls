<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\controllers;

use Carbon\Carbon;
use craft\web\Controller;
use Solspace\Commons\Helpers\PermissionHelper;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Resources\Bundles\BannerBundle;
use Solspace\Freeform\Resources\Bundles\CreateFormModalBundle;
use Solspace\Freeform\Resources\Bundles\DashboardBundle;
use Solspace\Freeform\Resources\Bundles\LogBundle;
use Solspace\SurveysPolls\FormTypes\Survey;
use Solspace\SurveysPolls\records\SurveyViewSettings;
use Solspace\SurveysPolls\Resources\bundles\SurveyResultsBundle;
use Solspace\SurveysPolls\SurveysPolls;
use yii\base\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class SurveysController extends Controller
{
    public function init(): void
    {
        parent::init();

        PermissionHelper::requirePermission(SurveysPolls::PERMISSION_SURVEYS_ACCESS);
    }

    public function actionIndex(): Response
    {
        $freeform = Freeform::getInstance();

        $formModels = $freeform->forms->getAllForms();

        $formIds = [];
        $formList = [];
        foreach ($formModels as $model) {
            if (Survey::class !== $model->type) {
                continue;
            }

            $formList[] = $model->getForm();
            $formIds[] = $model->id;
        }

        $submissionsService = $freeform->submissions;

        $totalSubmissions = $formIds ? $submissionsService->getSubmissionCount($formIds) : 0;
        $totalSpam = $formIds ? $submissionsService->getSubmissionCount($formIds, null, true) : 0;
        $totalSubmissionsByForm = $submissionsService->getSubmissionCountByForm(false);
        $totalSpamByForm = $submissionsService->getSubmissionCountByForm(true);

        if ($formModels) {
            $chartData = $freeform->charts
                ->getStackedAreaChartData(
                    new Carbon('-60 days'),
                    new Carbon('now'),
                    $formIds
                )
            ;
        } else {
            $chartData = $freeform->charts->getFakeStackedChartData();
        }

        $settingsService = $freeform->settings;

        $isSpamFolderEnabled = $settingsService->isSpamFolderEnabled();

        $integrations = $freeform->integrations->getAllIntegrations();

        \Craft::$app->view->registerAssetBundle(DashboardBundle::class);
        \Craft::$app->view->registerAssetBundle(LogBundle::class);
        \Craft::$app->view->registerAssetBundle(BannerBundle::class);
        \Craft::$app->view->registerAssetBundle(CreateFormModalBundle::class);

        $freeform->logger->registerJsTranslations($this->view);

        return $this->renderTemplate(
            'freeform-surveys-polls/surveys/dashboard',
            [
                'totalSubmissions' => $totalSubmissions,
                'totalSpam' => $totalSpam,
                'submissionsByForm' => $totalSubmissionsByForm,
                'spamByForm' => $totalSpamByForm,
                'forms' => $formList,
                'formCount' => \count($formList),
                'integrations' => $integrations,
                'logReader' => [],
                'isSpamFolderEnabled' => $isSpamFolderEnabled,
                'chartData' => $chartData,
                'updates' => [],
                'whatsNew' => [],
                'updatesLevel' => 'info',
            ]
        );
    }

    public function actionSettings(string $handle): Response
    {
        $request = \Craft::$app->getRequest();
        $form = $this->getForm($handle);

        if ($request->isPost) {
            PermissionHelper::requirePermission(SurveysPolls::PERMISSION_REPORTS_MANAGE);

            $fieldId = $request->post('fieldId');
            $chartType = $request->post('chartType', 'Horizontal');

            $field = Freeform::getInstance()->fields->getFieldById($fieldId);
            if (!$field) {
                throw new BadRequestHttpException('Field ID invalid');
            }

            $record = SurveyViewSettings::findOne([
                'formId' => $form->getId(),
                'fieldId' => $field->id,
            ]);

            if (!$record) {
                $record = new SurveyViewSettings();
                $record->formId = $form->getId();
                $record->fieldId = $field->id;
            }

            $record->chartType = $chartType;
            $record->save();

            $this->response->setStatusCode(201);

            return $this->asJson([
                'id' => $record->id,
                'chartType' => $record->chartType,
            ]);
        }

        /** @var SurveyViewSettings[] $settings */
        $settings = SurveyViewSettings::findAll(['formId' => $form->getId()]);

        $canModifyForm = PermissionHelper::checkPermission(Freeform::PERMISSION_FORMS_MANAGE.':'.$form->getId())
            || PermissionHelper::checkPermission(Freeform::PERMISSION_FORMS_MANAGE);

        $canViewSubmissions =
            PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_ACCESS)
            && (
                PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_MANAGE)
                || PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_MANAGE.':'.$form->getId())
                || PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_READ)
                || PermissionHelper::checkPermission(Freeform::PERMISSION_SUBMISSIONS_READ.':'.$form->getId())
            );

        $canManageReports = PermissionHelper::checkPermission(SurveysPolls::PERMISSION_REPORTS_MANAGE);

        return $this->asJson([
            'chartDefaults' => SurveysPolls::$plugin->getSettings()->chartDefaults,
            'highlightHighest' => SurveysPolls::$plugin->getSettings()->highlightHighest,
            'permissions' => [
                'form' => $canModifyForm,
                'submissions' => $canViewSubmissions,
                'reports' => $canManageReports,
            ],
            'fieldSettings' => array_map(
                function ($setting) {
                    return [
                        'id' => $setting->fieldId,
                        'chartType' => $setting->chartType,
                    ];
                },
                $settings
            ),
        ]);
    }

    public function actionResponseData(string $handle): Response
    {
        $form = $this->getForm($handle);

        return $this->asJson(SurveysPolls::$plugin->surveys->getChartData($form));
    }

    public function actionResults(string $handle): Response
    {
        $form = $this->getForm($handle);

        if ('application/json' === $this->request->headers->get('accept')) {
            return $this->asJson($form->getSurveyResults());
        }

        $this->view->registerAssetBundle(SurveyResultsBundle::class);

        return $this->renderTemplate(
            'freeform-surveys-polls/surveys/view-results',
            ['form' => $form]
        );
    }

    private function getForm(string $handle): Survey
    {
        $formModel = Freeform::getInstance()->forms->getFormByHandle($handle);
        if (!$formModel || Survey::class !== $formModel->type) {
            throw new NotFoundHttpException('Form does not exist');
        }

        // @var Survey $form
        return $formModel->getForm();
    }
}
