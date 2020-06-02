import React from 'react';
import {Error, Loading, ReferenceArrayInput, SelectArrayInput, useQueryWithStore} from 'react-admin';

export const ProjectSprintArrayInput = ({formData, scopedFormData, getSource, ...rest}) => {
  const {loading, error, data} = useQueryWithStore({
    type: 'getOne',
    resource: 'projects',
    payload: {id: scopedFormData.project}
  });

  if (loading) return <Loading/>;
  if (error) return <Error/>;
  if (!data) return null;

  const schedule = data.sprintSchedule;

  return (
    <ReferenceArrayInput {...rest} label="Sprints" source={getSource('sprints')} reference="sprints"
                         filter={{schedule: schedule}}>
      <SelectArrayInput/>
    </ReferenceArrayInput>
  );
}
