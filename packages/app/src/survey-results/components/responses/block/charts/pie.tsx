import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import { interpolateTurbo as colorScale } from 'd3-scale-chromatic';
import React from 'react';
import { Pie as PieChart } from 'react-chartjs-2';

import { ChartProps } from '@survey-app/survey-results/types/charts';
import { generateColor } from '@survey-app/utils/colors';

ChartJS.register(ArcElement, Tooltip, Legend);
ChartJS.defaults.plugins.legend.position = 'top';

export const Pie: React.FC<ChartProps> = ({ breakdown }) => {
  const labels = breakdown.map(({ label }) => label);
  const data = breakdown.map(({ votes }) => votes);
  const backgroundColor = breakdown.map(({ ranking }) => generateColor(ranking / breakdown.length, colorScale));

  return (
    <div>
      <PieChart
        data={{
          labels,
          datasets: [
            {
              label: 'Responses',
              data,
              backgroundColor,
            },
          ],
        }}
      />
    </div>
  );
};
