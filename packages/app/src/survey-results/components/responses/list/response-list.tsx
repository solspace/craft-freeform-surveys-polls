import React from 'react';

import { FieldData } from '@survey-app/survey-results/types/survey-data-types';
import translate from '@survey-app/utils/translations';

import { Heading } from '../../typography/heading';
import { Block } from '../block/block';
import { Wrapper } from './response-list.styles';

type Props = {
  submissions: number;
  results: FieldData[];
};

export const ResponseList: React.FC<Props> = ({ results, submissions }) => {
  return (
    <>
      <Heading>
        {translate('{count} Responses', { count: submissions })}{' '}
        <small>({translate('{count} questions', { count: results.length })})</small>
      </Heading>
      <Wrapper>
        {results.map((fieldResults, index) => (
          <Block key={fieldResults.field.id} {...fieldResults} responses={submissions} bulletin={index + 1} />
        ))}
      </Wrapper>
    </>
  );
};
