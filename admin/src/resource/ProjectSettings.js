import React, {Fragment} from 'react';
import {ArrayInput, AutocompleteInput, NumberInput, ReferenceInput, SelectInput, SimpleFormIterator} from 'react-admin'
import {ProjectSprintArrayInput} from './ProjectSprintArrayInput';
import {useForm} from 'react-final-form';

export const ProjectSettings = props => {
  const form = useForm();
  const resetDependentInputs = () => {
    form.change(props.getSource('sprints'), null);
    form.change(props.getSource('capacity'), null);
    form.change(props.getSource('defaultBacklogGroup'), null);
  }

  return (
    <Fragment>
      <ReferenceInput label="Project" source={props.getSource('project')} reference="projects"
                      onChange={resetDependentInputs}>
        <SelectInput/>
      </ReferenceInput>
      {props.scopedFormData && props.scopedFormData.project &&
      <ReferenceInput label="Default Backlog Group" source={props.getSource('defaultBacklogGroup')}
                      reference="backlog_groups">
        <AutocompleteInput/>
      </ReferenceInput>}
      {props.scopedFormData && props.scopedFormData.project && <ProjectSprintArrayInput {...props}/>}
      {props.scopedFormData && props.scopedFormData.sprints &&
      <ArrayInput label="Team Sprint Capacity" source={props.getSource('capacity')}>
        <SimpleFormIterator>
          <ReferenceInput label="Team" source="team" reference="teams">
            <AutocompleteInput/>
          </ReferenceInput>
          {!props.scopedFormData.isDefault &&
          <ReferenceInput label="Sprint" source="sprint" reference="sprints"
                          filter={{id: props.scopedFormData.sprints}} allowEmpty>
            <SelectInput emptyText="Any"/>
          </ReferenceInput>
          }
          <NumberInput label="Backend" source="backend"/>
          <NumberInput label="Frontend" source="frontend"/>
        </SimpleFormIterator>
      </ArrayInput>
      }
    </Fragment>
  );
}
