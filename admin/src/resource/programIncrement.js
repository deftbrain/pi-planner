import React from 'react';
import {Create, Datagrid, Edit, EditButton, List, ReferenceField, TextField} from 'react-admin'
import {ProgramIncrementForm} from './ProgramIncrementForm';

const ProgramIncrementCreate = props => (
  <Create {...props}>
    <ProgramIncrementForm/>
  </Create>
);

const Title = (props) => {
  return <span>Program increment {props.record.name}</span>;
};

const ProgramIncrementEdit = props => {
  return (
    <Edit {...props} title={<Title/>}>
      <ProgramIncrementForm/>
    </Edit>
  );
}

const ProgramIncrementList = props => (
  <List {...props}>
    <Datagrid>
      <TextField source="name"/>
      <ReferenceField source="project" reference="projects">
        <TextField source="name"/>
      </ReferenceField>
      <EditButton/>
    </Datagrid>
  </List>
);

export default {
  create: ProgramIncrementCreate,
  edit: ProgramIncrementEdit,
  list: ProgramIncrementList,
};
