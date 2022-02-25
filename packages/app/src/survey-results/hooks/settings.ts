import { useEffect, useState } from 'react';

import axios from '@survey-app/config/axios';
import { getUrlSegment } from '@survey-app/utils/urls';

import type { Settings } from '@survey-app/types/settings';

export const useSettings = (): Settings => {
  const [settings, setSettings] = useState<Settings>();

  useEffect(() => {
    axios
      .get<Settings>('/surveys-and-polls/' + getUrlSegment(1) + '/settings', {
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
      })
      .then((response) => response.data)
      .then(setSettings)
      .catch(console.error);
  }, []);

  return settings;
};
