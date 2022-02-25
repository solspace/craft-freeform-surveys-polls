import { SettingsContext } from '@survey-app/context/settings-context';
import React from 'react';
import { Form } from './components/form/form';
import { useSurveyResults } from './hooks/results';
import { useSettings } from './hooks/settings';
import { Wrapper } from './App.styles';

const App: React.FC = () => {
  const results = useSurveyResults();
  const settings = useSettings();

  if (results == undefined || settings === undefined) {
    return <div>Loading...</div>;
  }

  return (
    <Wrapper highlightHighest={settings.highlightHighest}>
      <SettingsContext.Provider value={settings}>
        <Form {...results} />
      </SettingsContext.Provider>
    </Wrapper>
  );
};

export default App;
