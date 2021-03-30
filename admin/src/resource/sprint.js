import React from 'react';
import {EditGuesser, FieldGuesser, InputGuesser, ListGuesser} from '@api-platform/admin';
import {TextField} from 'react-admin';

export const SprintList = props => (
  <ListGuesser {...props}>
    <FieldGuesser source="name"/>
    <FieldGuesser source="startDate"/>
    <FieldGuesser source="endDate"/>
  </ListGuesser>
);

export const SprintEdit = props => (
  <EditGuesser {...props}>
    <TextField source="name"/>
    <InputGuesser source="startDate"/>
    <InputGuesser source="endDate"/>
  </EditGuesser>
);
