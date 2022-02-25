import html2canvas from 'html2canvas';
import fileDownload from 'js-file-download';
import { useState } from 'react';

import axios from '@survey-app/config/axios';
import { trigger } from '@survey-app/utils/events';

import { ExportElementMap, TRIGGER_EXPORT } from '../response-data-chart';

export enum ExportType {
  Pdf = 'pdf',
  Images = 'images',
}

type DownloadExportType = () => [boolean, (type: ExportType) => void];

export const useDownloadExport: DownloadExportType = () => {
  const [loading, setLoading] = useState(false);

  const exportImages = async (type: ExportType): Promise<void> => {
    setLoading(true);
    const listOfItems: ExportElementMap = new Map();
    const event = trigger(TRIGGER_EXPORT, listOfItems);

    const imageData: string[] = [];
    for (const [_, item] of Array.from(event.detail.entries())) {
      imageData.push((await html2canvas(item)).toDataURL());
    }

    axios
      .post(`surveys-and-polls/export/${type}`, { imageData }, { responseType: 'blob' })
      .then((response) => {
        const filename = `exported.${type === ExportType.Pdf ? 'pdf' : 'zip'}`;

        fileDownload(response.data, filename);
      })
      .finally(() => setLoading(false));
  };

  return [loading, exportImages];
};
