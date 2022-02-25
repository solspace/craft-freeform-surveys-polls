import styled from 'styled-components';
import { ButtonSetWrapper } from './button';

export const Heading = styled.div`
  position: relative;

  display: block;
  padding: 0 0 40px;

  color: #3f4d5a;
  font-size: 1.5rem;
  font-weight: normal;

  small {
    color: #bbbdbe;
  }

  ${ButtonSetWrapper} {
    position: absolute;
    right: 0;
    top: 0;
  }
`;
