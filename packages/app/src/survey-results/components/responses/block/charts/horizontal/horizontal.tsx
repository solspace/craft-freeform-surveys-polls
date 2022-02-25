import React from 'react';

import { ChartProps } from '@survey-app/survey-results/types/charts';
import translate from '@survey-app/utils/translations';

import { Answer, Bar, Label, Percentage, Votes } from './horizontal.styles';

export const Horizontal: React.FC<ChartProps> = ({ breakdown }) => {
  return (
    <>
      {breakdown.map(({ label, value, votes, percentage, ranking }) => (
        <Answer key={value.toString()}>
          <Label>{label}</Label>
          <Votes>
            {votes} {translate('resp.')}
          </Votes>
          <Percentage>{Math.round(percentage)}%</Percentage>
          <Bar percentage={percentage} ranking={ranking} />
        </Answer>
      ))}
    </>
  );
};
