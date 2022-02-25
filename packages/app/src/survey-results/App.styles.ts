import styled from 'styled-components';

type WrapperProps = {
  highlightHighest: boolean;
};

export const Wrapper = styled.div<WrapperProps>`
  width: 100%;
  height: 100%;

  --highlight: ${({ highlightHighest }) => (highlightHighest ? '#e02e39' : '#33414d')};
`;
