import React from 'react';
import {
  AutocompleteInput,
  Datagrid,
  DeleteButton,
  EditButton,
  FormDataConsumer,
  FormTab,
  NumberField,
  ReferenceArrayInput,
  ReferenceField,
  ReferenceInput,
  ReferenceManyField,
  SelectArrayInput,
  TabbedForm,
  TextField,
  TextInput,
} from 'react-admin'

import {ProjectSprintArrayInput} from './ProjectSprintArrayInput';
import {useForm} from 'react-final-form';

export const ProgramIncrementForm = props => {
  return (
    <TabbedForm {...props}>
      <GeneralFormTab label="General"/>
      {props.record.id && <CapacityFormTab label="Capacity" path="capacity"/>}
    </TabbedForm>
  );
}

const GeneralFormTab = props => {
  const form = useForm();
  const resetProjectDependentInputs = () => {
    form.change('sprints', null);
  }

  return (
    <FormTab {...props}>
      <TextInput label="Name" source="name"/>
      <ReferenceInput label="Project" source="project" reference="projects"
                      onChange={resetProjectDependentInputs}>
        <AutocompleteInput/>
      </ReferenceInput>
      <ReferenceArrayInput label="Epic statuses" source="epicStatuses" reference="epic_statuses">
        <SelectArrayInput/>
      </ReferenceArrayInput>
      <FormDataConsumer>
        {({formData}) => formData.project
          &&
          <ProjectSprintArrayInput label="Sprints" source="sprints" reference="sprints"
                                   project={formData.project}/>
        }
      </FormDataConsumer>
    </FormTab>
  );
}

const CapacityFormTab = props => (
  <FormTab {...props}>
    <ReferenceManyField
      addLabel={false}
      reference="team_sprint_capacities"
      target="programIncrement"
    >
      <Datagrid>
        <ReferenceField source="team" reference="teams">
          <TextField source="name"/>
        </ReferenceField>
        <ReferenceField source="sprint" reference="sprints">
          <TextField source="name"/>
        </ReferenceField>
        <NumberField source="capacity.frontend" label="Frontend capacity"/>
        <NumberField source="capacity.backend" label="Backend capacity"/>
        <EditButton/>
        <DeleteButton redirect={false}/>
      </Datagrid>
    </ReferenceManyField>
  </FormTab>
);




