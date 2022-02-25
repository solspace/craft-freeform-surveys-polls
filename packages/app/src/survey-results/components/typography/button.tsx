import React from 'react';
import styled from 'styled-components';

import classes from '@survey-app/utils/classes';

type ButtonProps = {
  highlighted?: boolean;
  disabled?: boolean;
  loading?: boolean;
  onClick?: () => void;
};

const ButtonStyle = styled.button``;

const Button: React.FC<ButtonProps> = ({ highlighted, onClick, children, disabled, loading }) => {
  return (
    <ButtonStyle className={classes('btn', highlighted && 'submit')} onClick={onClick} disabled={disabled || loading}>
      {children}
    </ButtonStyle>
  );
};

type LinkProps = {
  highlighted?: boolean;
  url: string;
};

const Link: React.FC<LinkProps> = ({ highlighted, url, children }) => {
  return (
    <a href={url} className={classes('btn', highlighted && 'submit')}>
      {children}
    </a>
  );
};

interface ButtonSetInterface {
  Button: React.FC<ButtonProps>;
  Link: React.FC<LinkProps>;
}

export const ButtonSetWrapper = styled.div`
  display: flex;
  gap: 5px;

  font-size: 14px;
`;

export const ButtonSet: React.FC & ButtonSetInterface = ({ children }) => {
  return <ButtonSetWrapper>{children}</ButtonSetWrapper>;
};

ButtonSet.Button = Button;
ButtonSet.Link = Link;
