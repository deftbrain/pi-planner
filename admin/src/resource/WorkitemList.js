import React from 'react';
import {TextField} from 'react-admin';
import ListGuesser from '@api-platform/admin/lib/ListGuesser';

export const WorkitemList = props => (
  <ListGuesser {...props}>
    <TextField source="name"/>
    <TextField source="externalId"/>
  </ListGuesser>
);
