import React, {Component, Fragment} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {list, reset} from '../../actions/workitem/list';
import Board from 'react-trello';
import ExternalLink from '../entity/ExternalLink';
import './List.css'
import {update} from '../../actions/workitem/update';
import Estimate from './Estimate';
import upperFirst from 'lodash/upperFirst';

class List extends Component {
  static propTypes = {
    epic: PropTypes.object.isRequired,
    projects: PropTypes.array.isRequired,
    teams: PropTypes.object,
    sprints: PropTypes.object,
    programIncrement: PropTypes.object.isRequired,
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    deletedItem: PropTypes.object,
    list: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired,
    update: PropTypes.func.isRequired,
  };

  componentDidMount() {
    this.props.list(this.props.epic['@id']);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  onEstimateChange(workitem, event) {
    const value = event.target.value.length > 0 ? parseFloat(event.target.value) : null;
    const estimateFieldName = `estimate${upperFirst(event.target.name)}`;
    this.props.update(workitem, {[estimateFieldName]: value});
  }

  getBoardCards(workitems) {
    return workitems.map(workitem => {
      return {
        id: workitem['@id'],
        title: <ExternalLink entity={workitem}/>,
        label: <Estimate workitem={workitem} onChange={this.onEstimateChange.bind(this, workitem)}/>,
        tags: this.getWorkitemTags(workitem),
      };
    });
  }

  getWorkitems(filterCallback) {
    return this.props.retrieved[this.props.epic['@id']]['hydra:member'].filter(filterCallback);
  }

  getWorkitemTags(workitem) {
    const result = [];
    if (workitem['dependencies'].length > 0) {
      result.push({title: 'Dependencies: ' + workitem['dependencies'].length, bgcolor: 'red'});
    }
    if (workitem['dependants'].length > 0) {
      result.push({title: 'Dependants: ' + workitem['dependants'].length, bgcolor: 'orange'});
    }
    return result;
  }

  getBoardData() {
    const data = {};
    const columns = [];
    const unsignedColumnWidth = 15;
    const unsignedTeam = {'@id': null, 'name': 'Unsigned'};
    const unsignedSprint = {'@id': null, 'name': 'Unsigned'};

    for (let projectSettings of this.props.programIncrement.projectsSettings) {
      const involvedTeams = this.props.epic.teams;
      let teamsWithCapacity = new Set();
      for (let capacity of projectSettings.teamSprintCapacities) {
        if (involvedTeams.includes(capacity.team)) {
          teamsWithCapacity.add(capacity.team);
        }
      }

      if (!teamsWithCapacity.size) {
        continue;
      }

      let columnsPerRow = projectSettings.sprints.length;
      let columnWith = (100 - unsignedColumnWidth) / columnsPerRow;
      let teams = this.props.teams['hydra:member'].filter(t => teamsWithCapacity.has(t['@id']));
      teams = [unsignedTeam, ...teams];
      const sprints = [unsignedSprint, ...this.props.sprints['hydra:member'].filter(s => projectSettings.sprints.includes(s['@id']))];
      for (let team of teams) {
        let teamWorkitems = this.getWorkitems(w => w.team === team['@id']);
        for (let sprintIndex in sprints) {
          let sprint = sprints[sprintIndex];
          let teamSprintWorkitems = teamWorkitems.filter(w => w.sprint === sprint['@id']);
          columns.push({
            id: [this.props.epic['@id'], projectSettings.project, team['@id'], sprint['@id']].join(':'),
            title: 'Unsigned' === sprint.name ? team.name : '',
            label: 'Unsigned' === sprint.name ? sprint.name : 'S' + sprintIndex,
            cards: this.getBoardCards(teamSprintWorkitems),
            style: {
              width: `${'Unsigned' === sprint.name ? unsignedColumnWidth : columnWith}%`
            }
          });
        }
      }
    }

    data.lanes = columns;
    return data;
  }

  onDragEnd(cardId, sourceLaneId, targetLaneId) {
    // TODO: Replace with extracting info from a lane object
    const [, sourceProject] = sourceLaneId.split(':')
    const [, targetProject, targetTeam, targetSprint] = targetLaneId.split(':')
    const workitem = this.props.retrieved[this.props.epic['@id']]['hydra:member']
      .find(w => w['@id'] === cardId);
    let patch = {
      project: targetProject,
      team: targetTeam ? targetTeam : null,
      sprint: targetSprint ? targetSprint : null
    };
    if (targetProject !== sourceProject) {
      patch.backlogGroup = this.props.programIncrement.defaultBacklogGroup;
    }
    this.props.update(workitem, patch);
  }

  render() {
    return (
      <div>
        {this.props.loading && (
          <div className="alert alert-info">Loading...</div>
        )}
        {this.props.deletedItem && (
          <div className="alert alert-success">
            {this.props.deletedItem['@id']} deleted.
          </div>
        )}
        {this.props.error && (
          <div className="alert alert-danger">{this.props.error}</div>
        )}

        {this.props.retrieved && this.props.retrieved[this.props.epic['@id']] && this.props.teams && this.props.sprints && (
          <Board id={this.props.epic['@id']} data={this.getBoardData()} editable={true} laneDraggable={false}
                 handleDragEnd={this.onDragEnd.bind(this)}/>
        )}
      </div>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  const {
    retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  } = state.workitem.list;

  return {
    projects: state.project.list.retrieved['hydra:member'],
    teams: state.team.list.retrieved,
    sprints: state.sprint.list.retrieved,
    retrieved: retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  };
};

const mapDispatchToProps = dispatch => ({
  list: epic => dispatch(list(epic)),
  reset: eventSource => dispatch(reset(eventSource)),
  update: (workitem, values) => dispatch(update(workitem, values)),
});

export default connect(mapStateToProps, mapDispatchToProps)(List);
