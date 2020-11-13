import React from 'react';
import {Error, Loading, ReferenceArrayInput, SelectArrayInput, useQueryWithStore} from 'react-admin';

export const ProjectSprintArrayInput = ({project, ...rest}) => {
  const {loading, error, data} = useQueryWithStore({
    type: 'getOne',
    resource: 'projects',
    payload: {id: project}
  });

  if (loading) return <Loading/>;
  if (error) return <Error/>;
  if (!data) return null;

  return (
    <ReferenceArrayInput filter={{schedule: data.sprintSchedule}} {...rest} >
      <SelectArrayInput/>
    </ReferenceArrayInput>
  );
}
