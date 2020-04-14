import React from 'react';
import {
  CreateGuesser,
  EditGuesser,
  FieldGuesser,
  HydraAdmin,
  InputGuesser,
  ListGuesser,
  ResourceGuesser
} from '@api-platform/admin';
import {AutocompleteArrayInput, ReferenceArrayInput} from 'react-admin';

const ProgramIncrementList = props => (
  <ListGuesser {...props}>
    <FieldGuesser source={"name"}/>
  </ListGuesser>
);

const ProgramIncrementsCreate = props => (
  <CreateGuesser {...props}>
    <InputGuesser source="name"/>
    <ReferenceArrayInput source="projects"
                         reference="projects"
                         label="Projects"
                         filterToQuery={searchText => ({name: searchText})}>
      <AutocompleteArrayInput optionText="name"/>
    </ReferenceArrayInput>
  </CreateGuesser>
);

const ProgramIncrementsEdit = props => (
  <EditGuesser {...props}>
    <InputGuesser source="name"/>
    <ReferenceArrayInput source="projects"
                         reference="projects"
                         label="Projects"
                         filterToQuery={searchText => ({name: searchText})}>
      <AutocompleteArrayInput optionText="name"/>
    </ReferenceArrayInput>
  </EditGuesser>
);

export default () => (
  <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}>
    <ResourceGuesser name="program_increments"
                     list={ProgramIncrementList}
                     create={ProgramIncrementsCreate}
                     edit={ProgramIncrementsEdit}/>
    <ResourceGuesser name="projects"/>
  </HydraAdmin>
);
