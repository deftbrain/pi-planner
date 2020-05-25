import React from 'react';
import {Create, ReferenceInput, SelectInput, SimpleForm, NumberInput} from 'react-admin'
import {parse} from 'query-string';

export const TeamSprintCapacityCreate = props => {
  const {program_increment_id: programIncrementId} = parse(props.location.search);
  const programIncrementIdEncoded = programIncrementId ? encodeURIComponent(programIncrementId) : null;
  const redirectUrl = programIncrementIdEncoded ? `/program_increments/${programIncrementIdEncoded}/1` : 'list';

  return (
    <Create {...props}>
      <SimpleForm defaultValue={{programIncrement: programIncrementId}} redirect={redirectUrl}>
        <ReferenceInput label="Program Increment" source="programIncrement" reference="program_increments">
          <SelectInput optionText="name"/>
        </ReferenceInput>
        <ReferenceInput label="Team" source="team" reference="teams">
          <SelectInput optionText="name"/>
        </ReferenceInput>
        <ReferenceInput label="Sprint" source="sprint" reference="sprints">
          <SelectInput optionText="name"/>
        </ReferenceInput>
        <NumberInput source="capacityFrontend"/>
        <NumberInput source="capacityBackend"/>
      </SimpleForm>
    </Create>
  );
};
