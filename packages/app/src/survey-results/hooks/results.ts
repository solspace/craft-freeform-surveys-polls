import { useEffect, useState } from 'react';

import axios from '@survey-app/config/axios';
import { getUrlSegment } from '@survey-app/utils/urls';
import type { SurveyData } from '@survey-app/survey-results/types/survey-data-types';

export const useSurveyResults = () => {
  const [results, setResults] = useState<SurveyData>();

  useEffect(() => {
    axios
      .get<SurveyData>('/surveys-and-polls/' + getUrlSegment(1), {
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
      })
      .then((response) => response.data)
      .then(setResults)
      .catch(console.error);
  }, []);

  return results;
};
