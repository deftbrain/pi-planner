import React from 'react';
import {Create, Edit, NumberInput, ReferenceInput, SelectInput, SimpleForm} from 'react-admin'
import {ProjectSprintReferenceInput} from './ProjectSprintReferenceInput';
import {parse} from 'query-string';

const Form = props => {
  const queryParams = parse(props.location.search);
  const programIncrement = props.record.programIncrement || queryParams.programIncrement;
  const redirect = `/program_increments/${encodeURIComponent(programIncrement)}/capacity`;

  return (
    <SimpleForm {...props} initialValues={{programIncrement}} redirect={redirect}>
      <ReferenceInput source="programIncrement" reference="program_increments">
        <SelectInput source="name"/>
      </ReferenceInput>
      <ReferenceInput source="team" reference="teams">
        <SelectInput source="name"/>
      </ReferenceInput>
      <ProjectSprintReferenceInput programIncrement={programIncrement} source="sprint" reference="sprints"/>
      <NumberInput source="capacity.frontend" label="Frontend capacity"/>
      <NumberInput source="capacity.backend" label="Backend capacity"/>
    </SimpleForm>
  );
}

export const TeamSprintCapacityCreate = props => <Create {...props}><Form location={props.location}/></Create>;
export const TeamSprintCapacityEdit = props => <Edit {...props}><Form location={props.location}/></Edit>;
