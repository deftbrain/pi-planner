import React from 'react';
import {Error, Loading, ReferenceInput, SelectInput, useQueryWithStore} from 'react-admin';

export const ProjectSprintReferenceInput = ({programIncrement, ...rest}) => {
  const {loading, error, data} = useQueryWithStore({
    type: 'getOne',
    resource: 'program_increments',
    payload: {id: programIncrement}
  });

  if (loading) return <Loading/>;
  if (error) return <Error/>;
  if (!data) return null;

  return (
    <ReferenceInput filter={{id: data.sprints}} {...rest}>
      <SelectInput/>
    </ReferenceInput>
  );
}
