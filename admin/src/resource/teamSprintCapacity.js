import React from 'react';
import {AutocompleteInput, Create, Edit, NumberInput, ReferenceInput, SimpleForm} from 'react-admin'
import {ProjectSprintReferenceInput} from './ProjectSprintReferenceInput';
import {parse} from 'query-string';

const Form = props => {
  const queryParams = parse(props.location.search);
  const projectSettings = props.record.projectSettings || queryParams.projectSettings;
  const redirect = `/project_settings/${encodeURIComponent(projectSettings)}/show/capacity`;

  return (
    <SimpleForm {...props} initialValues={{projectSettings}} redirect={redirect}>
      <ReferenceInput source="team" reference="teams">
        <AutocompleteInput source="name"/>
      </ReferenceInput>
      <ProjectSprintReferenceInput projectSettings={projectSettings} source="sprint" reference="sprints" allowEmpty/>
      <NumberInput source="capacity.frontend" label="Frontend capacity"/>
      <NumberInput source="capacity.backend" label="Backend capacity"/>
    </SimpleForm>
  );
}

export const TeamSprintCapacityCreate = props => <Create {...props}><Form location={props.location}/></Create>;
export const TeamSprintCapacityEdit = props => <Edit {...props}><Form location={props.location}/></Edit>;
