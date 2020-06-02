import React from 'react';
import {Edit} from 'react-admin'
import {ProgramIncrementForm} from './ProgramIncrementForm';

export const ProgramIncrementEdit = props => (
  <Edit {...props}>
    <ProgramIncrementForm/>
  </Edit>
);
