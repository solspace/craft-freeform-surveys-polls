import React from 'react';

import CheckboxGroupSvg from '@survey-app/assets/field-type-icons/checkbox-group.svg';
import MultipleSelectSvg from '@survey-app/assets/field-type-icons/multiple-select.svg';
import OpinionScaleSvg from '@survey-app/assets/field-type-icons/opinion-scale.svg';
import RadioGroupSvg from '@survey-app/assets/field-type-icons/radio-group.svg';
import RatingSvg from '@survey-app/assets/field-type-icons/rating.svg';
import SelectSvg from '@survey-app/assets/field-type-icons/select.svg';
import TextSvg from '@survey-app/assets/field-type-icons/text.svg';

export const getIcon = (type: string): React.ReactNode => {
  switch (type) {
    case 'checkbox_group':
      return <CheckboxGroupSvg />;

    case 'radio_group':
      return <RadioGroupSvg />;

    case 'rating':
      return <RatingSvg />;

    case 'select':
      return <SelectSvg />;

    case 'multiple_select':
      return <MultipleSelectSvg />;

    case 'opinion_scale':
      return <OpinionScaleSvg />;
  }

  return <TextSvg />;
};
