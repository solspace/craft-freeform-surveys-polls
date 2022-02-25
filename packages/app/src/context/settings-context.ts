import type { Settings } from '@survey-app/types/settings';
import { createContext } from 'react';

export const SettingsContext = createContext<Settings>(null);
