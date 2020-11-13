import React from 'react';
import {Create, Edit, NumberInput, ReferenceInput, SelectInput, SimpleForm} from 'react-admin'
import {ProjectSprintReferenceInput} from './ProjectSprintReferenceInput';
import {parse} from 'query-string';

export const TeamSprintCapacityCreate = props => {
  const {programIncrement} = parse(props.location.search);
  const redirect = programIncrement ? `/program_increments/${encodeURIComponent(programIncrement)}/capacity` : 'show';
  // TODO: Figure out why there is redirection to /undefined after creation of a capacity
  // in case when repeated code is moved into TeamSprintCapacityForm component
  return (
    <Create {...props}>
      <SimpleForm initialValues={{programIncrement}} redirect={redirect}>
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
    </Create>
  );
}

export const TeamSprintCapacityEdit = props => {
  const {programIncrement} = parse(props.location.search);
  const redirect = programIncrement ? `/program_increments/${encodeURIComponent(programIncrement)}/capacity` : 'show';
  // TODO: Figure out why there is redirection to /undefined after creation of a capacity
  // in case when repeated code is moved into TeamSprintCapacityForm component
  return (
    <Edit {...props}>
      <SimpleForm initialValues={{programIncrement}} redirect={redirect}>
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
    </Edit>
  );
}
