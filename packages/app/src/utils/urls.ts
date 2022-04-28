export const baseUrl = window.location.href.replace(/.*(\/[^/]+\/freeform).*/i, '$1');
export const segments = window.location.href.replace(/.*\/[^/]+\/freeform\/([^?]*)?(\?.*)?/i, '$1').split('/');

export const generateUrl = (url?: string): string => {
  url = (url ?? '')
    .replace(/\/+/g, '/')
    .replace(/^\/(.*)/, '$1')
    .replace(/\/$/, '');

  url = url.length ? `/${url}` : '';

  return `${baseUrl}${url}`;
};

type GetUrlSegmentType = (number: number) => string | undefined;

export const getUrlSegment: GetUrlSegmentType = (number) => segments[number];
