import { useContext } from 'react';

import { SettingsContext } from '@survey-app/context/settings-context';
import { FieldSetting } from '@survey-app/types/settings';
import { Field } from '@survey-results/types/survey-data-types';

export const useFieldSetting = (field: Field): FieldSetting | undefined => {
  const settings = useContext(SettingsContext);

  const fieldSetting = settings.fieldSettings.find((item) => item.id === field.id);
  if (fieldSetting) {
    return fieldSetting;
  }

  return {
    id: field.id,
    chartType: settings.chartDefaults[field.type],
  };
};
