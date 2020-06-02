import React from 'react';
import {Create} from 'react-admin'
import {ProgramIncrementForm} from './ProgramIncrementForm';

export const ProgramIncrementCreate = props => (
  <Create {...props}>
    <ProgramIncrementForm/>
  </Create>
);
