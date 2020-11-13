import React from 'react';
import {Button, Create, Datagrid, Edit, EditButton, List, ReferenceField, TextField, TopToolbar} from 'react-admin'
import {ProgramIncrementForm} from './ProgramIncrementForm';
import GroupAdd from "@material-ui/icons/GroupAdd";
import {Link} from 'react-router-dom';


const ProgramIncrementCreate = props => (
  <Create {...props}>
    <ProgramIncrementForm/>
  </Create>
);

const Title = (props) => {
  return <span>Program increment {props.record.name}</span>;
};

const EditActions = ({id}) => {
  return (
    <TopToolbar>
      <Button
        component={Link}
        to={{
          pathname: "/team_sprint_capacities/create",
          search: `?programIncrement=${id}`,
        }}
        label="Add team capacity"
      >
        <GroupAdd/>
      </Button>
    </TopToolbar>
  );
}

const ProgramIncrementEdit = props => {
  return (
    // Pass record ID directly to EditActions because its data property is undefined for some reason
    <Edit {...props} title={<Title/>} actions={<EditActions id={props.id}/>}>
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
