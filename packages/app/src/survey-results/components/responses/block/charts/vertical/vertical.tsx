import { ChartProps } from '@survey-app/survey-results/types/charts';
import translate from '@survey-app/utils/translations';
import React from 'react';
import { Answer, Bar, Label, Percentage, Votes, Wrapper } from './vertical.styles';

export const Vertical: React.FC<ChartProps> = ({ breakdown }) => {
  return (
    <Wrapper count={breakdown.length}>
      {breakdown.map(({ label, value, votes, percentage, ranking }) => (
        <Answer key={value.toString()}>
          <Percentage>{Math.round(percentage)}%</Percentage>
          <Votes>
            {votes} {translate('resp.')}
          </Votes>
          <Bar percentage={percentage} ranking={ranking} />

          <Label>{label}</Label>
        </Answer>
      ))}
    </Wrapper>
  );
};
