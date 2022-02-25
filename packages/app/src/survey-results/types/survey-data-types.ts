import { FieldType } from '@survey-app/types/field-types';

export type Form = {
  id: number;
  name: string;
  handle: string;
  color: string;
  submissions: number;
  spam?: number;
};

export type Field = {
  id: number;
  label: string;
  handle: string;
  type: FieldType;
  multiChoice: boolean;
};

export type Breakdown = {
  label: string;
  value: string | number | boolean;
  votes: number;
  ranking: number;
  percentage: number;
};

export type FieldData = {
  field: Field;
  breakdown: Breakdown[];
  votes: number;
  skipped: number;
  average?: number;
  max?: number;
};

export type SurveyData = {
  form: Form;
  votes: number;
  results: FieldData[];
};
