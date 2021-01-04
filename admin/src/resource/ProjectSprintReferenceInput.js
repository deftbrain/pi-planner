import React from 'react';
import {Error, Loading, ReferenceInput, SelectInput, useQueryWithStore} from 'react-admin';

export const ProjectSprintReferenceInput = ({projectSettings, ...rest}) => {
  const {loading, error, data} = useQueryWithStore({
    type: 'getOne',
    resource: 'project_settings',
    payload: {id: projectSettings}
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
