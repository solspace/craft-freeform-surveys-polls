import 'chartjs-adapter-date-fns';

import {
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  TimeScale,
  Title,
  Tooltip,
} from 'chart.js';
import React, { useContext } from 'react';
import { Line } from 'react-chartjs-2';

import { SettingsContext } from '@survey-app/context/settings-context';
import { Form } from '@survey-app/survey-results/types/survey-data-types';
import translate from '@survey-app/utils/translations';
import { generateUrl } from '@survey-app/utils/urls';

import { ButtonSet } from '../../typography/button';
import { Heading } from '../../typography/heading';
import { ExportType, useDownloadExport } from './hooks/download-export';
import { useResponseData } from './hooks/response-data';
import { Wrapper } from './response-data-chart.styles';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, TimeScale);

type Props = {
  form: Form;
};

export type ExportElementMap = Map<number, HTMLElement>;
export const TRIGGER_EXPORT = 'trigger-export';

export const ResponseDataChart: React.FC<Props> = ({ form }) => {
  const responseData = useResponseData();
  const [loading, exportImages] = useDownloadExport();
  const { permissions } = useContext(SettingsContext);

  return (
    <Wrapper>
      <Heading>
        {form.name}

        <ButtonSet>
          {permissions.form && (
            <ButtonSet.Link url={generateUrl(`/forms/${form.id}`)}>{translate('Edit Form')}</ButtonSet.Link>
          )}

          {permissions.submissions && (
            <ButtonSet.Link url={generateUrl(`/submissions/${form.handle}`)}>
              {translate('View Responses')}
            </ButtonSet.Link>
          )}

          {permissions.submissions && form.spam !== null && (
            <ButtonSet.Link url={generateUrl(`/spam/${form.handle}`)} highlighted>
              {translate('Spam ({spam})', { spam: form.spam })}
            </ButtonSet.Link>
          )}

          <ButtonSet.Button loading={loading} disabled={loading} onClick={(): void => exportImages(ExportType.Pdf)}>
            {translate('Export PDF')}
          </ButtonSet.Button>
        </ButtonSet>
      </Heading>

      <div>
        <Line
          height={50}
          data={{
            datasets: [
              {
                label: 'Submissions',
                data: responseData.data,
                backgroundColor: form.color,
                borderColor: form.color,
                tension: 0.4,
              },
            ],
          }}
          options={{
            responsive: true,
            scales: {
              y: {
                min: 0,
                ticks: {
                  maxTicksLimit: 6,
                  stepSize: 1,
                },
              },
              x: {
                type: 'time',
                grid: {
                  display: false,
                },
                time: {
                  tooltipFormat: 'MMM d',
                  minUnit: 'day',
                  displayFormats: {
                    day: 'MMM d',
                  },
                },
              },
            },
            plugins: {
              legend: {
                display: false,
                position: 'top',
              },
            },
          }}
        />
      </div>
    </Wrapper>
  );
};
