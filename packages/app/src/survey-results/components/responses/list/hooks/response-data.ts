import { useEffect, useState } from 'react';

import axios from '@survey-app/config/axios';
import { getUrlSegment } from '@survey-app/utils/urls';

import { ResponseData } from '../types/response-data';

export const useResponseData = (): ResponseData => {
  const [data, setData] = useState<ResponseData>({
    labels: [],
    data: [],
  });

  useEffect(() => {
    axios
      .get<ResponseData>('surveys-and-polls/' + getUrlSegment(1) + '/response-data')
      .then((response) => response.data)
      .then(setData)
      .catch(console.error);
  }, []);

  return data;
};
