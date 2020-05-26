import React from 'react';
import {
  Create,
  Datagrid,
  Edit,
  FormTab,
  ReferenceArrayInput,
  ReferenceField,
  ReferenceManyField,
  SelectArrayInput,
  TabbedForm,
  TextField,
  TextInput
} from 'react-admin'
import Button from '@material-ui/core/Button';
import {Link} from 'react-router-dom';
import {stringify} from 'query-string';
import {FieldGuesser, ListGuesser} from "@api-platform/admin";

export const ProgramIncrementList = props => (
  <ListGuesser {...props}>
    <FieldGuesser source="name"/>
    <FieldGuesser source="projects"/>
  </ListGuesser>
);

export const ProgramIncrementCreate = props => (
  <Create {...props}>
    <TabbedForm>
      <FormTab label="General">
        <TextInput label="Name" source={"name"}/>
        <ReferenceArrayInput label="Projects" source="projects" reference="projects">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
        <ReferenceArrayInput label="Teams" source="teams" reference="teams">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
        <ReferenceArrayInput label="Sprints" source="sprints" reference="sprints">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
      </FormTab>
    </TabbedForm>
  </Create>
);

const AddCapacityButton = ({record}) => (
  <Button
    component={Link}
    to={{
      pathname: '/team_sprint_capacities/create',
      search: stringify({program_increment_id: record.id}),
    }}
  >
    Add Capacity
  </Button>
);

export const ProgramIncrementEdit = props => (
  <Edit {...props}>
    <TabbedForm>
      <FormTab label="General">
        <TextInput label="Name" source={"name"}/>
        <ReferenceArrayInput label="Projects" source="projects" reference="projects">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
        <ReferenceArrayInput label="Teams" source="teams" reference="teams">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
        <ReferenceArrayInput label="Sprints" source="sprints" reference="sprints">
          <SelectArrayInput optionText="name"/>
        </ReferenceArrayInput>
      </FormTab>
      <FormTab label="Team Capacity">
        <ReferenceManyField
          addLabel={false}
          reference="team_sprint_capacities"
          target="program_increment"
        >
          <Datagrid>
            <ReferenceField source="team" reference="teams">
              <TextField source="name"/>
            </ReferenceField>
            <ReferenceField source="sprint" reference="sprints">
              <TextField source="name"/>
            </ReferenceField>
            <TextField source="capacityFrontend"/>
            <TextField source="capacityBackend"/>
          </Datagrid>
        </ReferenceManyField>
        <AddCapacityButton/>
      </FormTab>
    </TabbedForm>
  </Edit>
);
