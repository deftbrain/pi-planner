import React from 'react';
import {EditGuesser} from '@api-platform/admin';
import {TextField} from 'react-admin';

export const WorkitemEdit = props => (
  <EditGuesser {...props}>
    <TextField source="name"/>
  </EditGuesser>
);
