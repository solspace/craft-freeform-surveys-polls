import styled from 'styled-components';

export enum Icon {
  CheckboxGroup,
  RadioGroup,
  Select,
  MultiSelect,
  Text,
  Rating,
}

export const Wrapper = styled.li`
  display: grid;
  grid-template-columns: 42px auto;
  grid-template-rows: auto auto;
  grid-template-areas:
    'bulletin label'
    'settings numbers';
  gap: 10px;

  &:not(:last-child) {
    margin-bottom: 42px;
  }
`;

type BulletinProps = {
  icon?: Icon;
};

export const Bulletin = styled.div<BulletinProps>`
  grid-area: bulletin;

  padding-top: 5px;

  background-color: #f3f7fd;
  border-radius: 4px;

  color: #df2733;
  text-align: center;
  font-size: 24px;
  font-weight: bold;

  &:hover {
    transition: background-color 0.2s ease-out;
    background-color: #e0e4e9;
  }

  span {
    white-space: nowrap;
  }

  svg {
    display: block;

    margin: 3px auto;

    width: 28px;
  }
`;

export const Settings = styled.div`
  grid-area: settings;

  position: relative;
`;

export const SettingsButton = styled.button`
  display: block;

  width: 100%;
  padding: 5px 12px;

  border-radius: 4px;
  color: #ced6df;

  transition: all 0.2s ease-out;

  &:hover,
  &.open {
    background-color: #c8cfd5;
    color: #ffffff;
  }

  &.open {
    border-radius: 4px 4px 0 0;
  }

  @keyframes rotator {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  &.loading svg {
    animation-name: rotator;
    animation-duration: 3s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
  }
`;

export const DropdownWrapper = styled.div`
  position: absolute;
  left: 0;
  top: 28px;
  z-index: 2;

  width: 150px;
  overflow: hidden;

  border-radius: 0 5px 5px 5px;
  border: 1px solid #e0e2e6;
  background: #ffffff;

  box-shadow: rgba(17, 17, 26, 0.1) 5px 5px 8px;
`;

export const DropdownItem = styled.a`
  display: block;
  padding: 3px 10px;

  background-color: #ffffff;
  color: #000000;
  font-size: 12px;

  transition: background-color 0.2s ease-out;

  &.selected {
    background-color: #f3f7fd;
  }

  &:hover {
    background-color: #d8dce1;
    text-decoration: none;
  }
`;

export const Label = styled.div`
  grid-area: label;
`;

export const Heading = styled.div`
  font-size: 24px;
  font-weight: bold;

  margin: 5px 0 8px;
`;

export const SubHeading = styled.div`
  position: relative;

  font-size: 12px;
  color: #ccc;
`;

export const Extras = styled.div`
  position: absolute;
  right: 0;
  top: 0;
`;

export const Numbers = styled.div`
  grid-area: numbers;
`;

export const HiddenBlock = styled.li`
  position: relative;

  padding: 3px 0;
  margin-bottom: 42px;

  background: #f3f7fd;
  text-align: center;
  font-size: 12px;

  ${SettingsButton} {
    position: absolute;
    left: 0;
    top: 0;

    width: 40px;
  }
`;
