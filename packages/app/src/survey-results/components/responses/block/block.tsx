import React, { useContext, useEffect, useRef, useState } from 'react';

import SettingsSvg from '@survey-app/assets/misc/settings.svg';
import axios from '@survey-app/config/axios';
import { SettingsContext } from '@survey-app/context/settings-context';
import { ChartProps } from '@survey-app/survey-results/types/charts';
import { FieldData } from '@survey-app/survey-results/types/survey-data-types';
import { Chart } from '@survey-app/types/chart-types';
import { FieldType } from '@survey-app/types/field-types';
import { FieldSetting } from '@survey-app/types/settings';
import classes from '@survey-app/utils/classes';
import { off, on } from '@survey-app/utils/events';
import translate from '@survey-app/utils/translations';
import { getUrlSegment } from '@survey-app/utils/urls';

import { ExportElementMap, TRIGGER_EXPORT } from '../list/response-data-chart';
import {
  Bulletin,
  DropdownItem,
  DropdownWrapper,
  Extras,
  Heading,
  HiddenBlock,
  Label,
  Numbers,
  Settings,
  SettingsButton,
  SubHeading,
  Wrapper,
} from './block.styles';
import * as charts from './charts';
import { Average } from './components/average';
import { useFieldSetting } from './hooks/field-setting';
import { getIcon } from './icon';

type Props = FieldData & {
  bulletin: number;
  responses: number;
};

const excludedExportTypes = [Chart.Hidden, Chart.Text];
const textTypes: FieldType[] = [
  FieldType.Text,
  FieldType.Textarea,
  FieldType.Email,
  FieldType.Number,
  FieldType.Phone,
  FieldType.Regex,
  FieldType.Website,
];

export const Block: React.FC<Props> = ({ field, responses, breakdown, skipped, bulletin, average, max }) => {
  const [chartType, setChartType] = useState<Chart>(Chart.Horizontal);
  const [loading, setLoading] = useState(false);
  const [showSettings, setShowSettings] = useState(false);
  const settings = useFieldSetting(field);
  const { permissions } = useContext(SettingsContext);

  const ref = useRef<HTMLLIElement>();

  useEffect(() => {
    let chartType = settings.chartType;
    if (!textTypes.includes(field.type)) {
      if (breakdown.length > 10) {
        chartType = Chart.Horizontal;
      }
    }

    setChartType(chartType);
  }, []);

  useEffect(() => {
    if (excludedExportTypes.includes(chartType)) {
      return;
    }

    const callback = (event: CustomEvent<ExportElementMap>): void => {
      event.detail.set(field.id, ref.current);
    };

    on(TRIGGER_EXPORT, callback);

    return (): void => {
      off(TRIGGER_EXPORT, callback);
    };
  }, [ref, chartType]);

  const handleSetting = (type: Chart): void => {
    setLoading(true);
    setChartType(type);

    axios
      .post<FieldSetting>('/surveys-and-polls/' + getUrlSegment(1) + '/settings', {
        fieldId: field.id,
        chartType: type,
      })
      .finally(() => setLoading(false));
  };

  const ChartElement: React.FC<ChartProps> = charts[chartType];
  const chartOptions = Object.keys(Chart).filter((item) => {
    if (textTypes.includes(field.type)) {
      return [Chart.Text, Chart.Hidden].includes(item as Chart);
    }

    return (item as Chart) !== Chart.Text;
  });

  const SettingsBlock = (): JSX.Element => (
    <SettingsButton
      className={classes(loading && 'loading', showSettings && 'open')}
      onClick={(): void => setShowSettings(!showSettings)}
    >
      <SettingsSvg />
      {showSettings && (
        <DropdownWrapper>
          {chartOptions.map((type) => (
            <DropdownItem
              key={type}
              className={chartType === type && 'selected'}
              onClick={(): void => handleSetting(type as Chart)}
            >
              {type}
            </DropdownItem>
          ))}
        </DropdownWrapper>
      )}
    </SettingsButton>
  );

  if (chartType === Chart.Hidden) {
    return (
      <HiddenBlock>
        {permissions.reports && <SettingsBlock />}
        --{' '}
        <span
          dangerouslySetInnerHTML={{
            __html: translate('Question <b>{index}</b> Hidden', {
              index: bulletin,
            }),
          }}
        ></span>{' '}
        --
      </HiddenBlock>
    );
  }

  return (
    <Wrapper ref={ref}>
      <Bulletin>
        <span>{bulletin}</span>
        {getIcon(field.type)}
      </Bulletin>
      <Label>
        <Heading>{field.label}</Heading>
        <SubHeading>
          {responses - skipped} {translate('answered')}, {skipped} {translate('skipped')}
          {field.multiChoice && <Extras>{translate('multiple choice')}</Extras>}
        </SubHeading>
        <Average average={average} max={max} />
      </Label>
      <Settings>{permissions.reports && <SettingsBlock />}</Settings>
      <Numbers>
        <ChartElement breakdown={breakdown} />
      </Numbers>
    </Wrapper>
  );
};
