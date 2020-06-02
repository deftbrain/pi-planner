import React from 'react';
import {TextField} from 'react-admin';
import ListGuesser from '@api-platform/admin/lib/ListGuesser';

export const ProgramIncrementList = props => (
  <ListGuesser {...props}>
    <TextField source="name"/>
  </ListGuesser>
);
