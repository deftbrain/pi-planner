import React from 'react';
import {Create, Datagrid, DeleteButton, Edit, EditButton, List, SimpleForm, TextField, TextInput} from 'react-admin';

const Form = props => (
  <SimpleForm {...props} redirect="list">
    <TextInput source="name"/>
  </SimpleForm>
);

const ProgramIncrementCreate = props => <Create {...props}><Form/></Create>;
const ProgramIncrementEdit = props => <Edit {...props}><Form/></Edit>;
const ProgramIncrementList = props => (
  <List {...props}>
    <Datagrid>
      <TextField source="name"/>
      <EditButton/>
      <DeleteButton/>
    </Datagrid>
  </List>
);

export default {
  create: ProgramIncrementCreate,
  edit: ProgramIncrementEdit,
  list: ProgramIncrementList,
};
