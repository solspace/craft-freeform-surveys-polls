<?php

namespace Solspace\SurveysPolls\controllers;

use craft\web\Controller;
use Solspace\Commons\Helpers\PermissionHelper;
use Solspace\Commons\Helpers\StringHelper;
use Solspace\Freeform\Freeform;
use Solspace\SurveysPolls\SurveysPolls;
use yii\base\Response;

class SettingsController extends Controller
{
    public function actionIndex(): Response
    {
        $settings = SurveysPolls::$plugin->getSettings();

        if ('application/json' === $this->request->headers->get('accept')) {
            return $this->asJson($settings);
        }

        return $this->renderTemplate(
            'freeform-surveys-polls/settings',
            ['settings' => $settings]
        );
    }

    public function actionSave()
    {
        PermissionHelper::requirePermission(Freeform::PERMISSION_SETTINGS_ACCESS);

        $this->requirePostRequest();

        $request = \Craft::$app->request;

        $chartDefaults = $request->post('chartDefaults');
        $highlightHighest = $request->post('highlightHighest', true);

        $data = [
            'chartDefaults' => $chartDefaults,
            'highlightHighest' => (bool) $highlightHighest,
        ];

        $plugin = SurveysPolls::$plugin;
        $plugin->setSettings($data);

        if (\Craft::$app->plugins->savePluginSettings($plugin, $data)) {
            \Craft::$app->session->setNotice(Freeform::t('Settings Saved'));

            return $this->redirectToPostedUrl();
        }

        $errors = $plugin->getSettings()->getErrors();
        \Craft::$app->session->setError(
            implode("\n", StringHelper::flattenArrayValues($errors))
        );
    }
}
