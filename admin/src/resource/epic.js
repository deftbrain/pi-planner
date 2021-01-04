import React from 'react';
import {
  AutocompleteArrayInput,
  ChipField,
  Edit,
  ReferenceArrayInput,
  ReferenceField,
  SimpleForm,
  TextInput,
} from 'react-admin';


const EpicEdit = props => (
  <Edit {...props}>
    <Form/>
  </Edit>
);

const Form = props => {
  const redirect = `/project_settings/${encodeURIComponent(props.record.projectSettings)}/show/epics`;

  return (
    <SimpleForm {...props} redirect={redirect}>
      <TextInput source="name" disabled />
      <ReferenceArrayInput label="Teams" source="teams" reference="teams">
        <AutocompleteArrayInput/>
      </ReferenceArrayInput>
    </SimpleForm>
  );
};

export default {
  edit: EpicEdit,
  // list: EpicList,
};
