import React from 'react';
import { Scrollbars } from 'react-custom-scrollbars';

import { ChartProps } from '@survey-app/survey-results/types/charts';

import { Item, Wrapper } from './text.styles';

export const Text: React.FC<ChartProps> = ({ breakdown }) => {
  return (
    <Wrapper>
      <Scrollbars style={{ height: 250 }}>
        {breakdown.map((item) => (
          <Item key={item.value.toString()}>
            {item.label}
            {item.votes > 1 && ` (${item.votes})`}
          </Item>
        ))}
      </Scrollbars>
    </Wrapper>
  );
};
