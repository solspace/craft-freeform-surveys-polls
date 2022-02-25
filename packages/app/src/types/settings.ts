import { FieldType } from '@survey-app/types/field-types';

import { Chart } from './chart-types';

export type FieldSetting = {
  id: number;
  chartType: Chart;
};

export type Settings = {
  highlightHighest: boolean;
  fieldSettings: FieldSetting[];
  permissions: {
    form: boolean;
    submissions: boolean;
    reports: boolean;
  };
  chartDefaults: {
    [key in FieldType]: Chart;
  };
};
