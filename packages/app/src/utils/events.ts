type EventListenerType = (
  name: string,
  listener: EventListenerOrEventListenerObject,
  element?: HTMLElement | Document
) => void;

export const on: EventListenerType = (name, listener, element = document) => {
  element.addEventListener(name, listener);
};

export const off: EventListenerType = (name, listener, element = document) => {
  element.removeEventListener(name, listener);
};

type TriggerType = <T>(name: string, detail?: T, element?: HTMLElement | Document) => CustomEvent<T>;

export const trigger: TriggerType = (name, detail, element = document) => {
  const event = new CustomEvent(name, { detail });
  element.dispatchEvent(event);

  return event;
};
