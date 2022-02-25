import React from 'react';

import { SurveyData } from '@survey-app/survey-results/types/survey-data-types';

import { ResponseDataChart } from '../responses/list/response-data-chart';
import { ResponseList } from '../responses/list/response-list';

export const Form: React.FC<SurveyData> = ({ form, results }) => {
  return (
    <div>
      <ResponseDataChart form={form} />
      <ResponseList submissions={form.submissions} results={results} />
    </div>
  );
};
