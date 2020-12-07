import React from 'react';
import ListGuesser from '@api-platform/admin/lib/ListGuesser';
import FieldGuesser from '@api-platform/admin/lib/FieldGuesser';
import EditGuesser from '@api-platform/admin/lib/EditGuesser';
import {ReferenceArrayInput, SelectArrayInput, TextInput} from 'react-admin';

const EpicList = props => (
  <ListGuesser {...props}>
    <FieldGuesser source={'name'}/>
    <FieldGuesser source={'status'}/>
    <FieldGuesser source={'project'}/>
    <FieldGuesser source={'teams'}/>
  </ListGuesser>
);

const EpicEdit = props => (
  <EditGuesser {...props}>
    <TextInput source={'name'} disabled label="Name"/>
    <ReferenceArrayInput source="teams" reference="teams" label="Teams">
      <SelectArrayInput/>
    </ReferenceArrayInput>
  </EditGuesser>
);

export default {
  list: EpicList,
  edit: EpicEdit,
};
