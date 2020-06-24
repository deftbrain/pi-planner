import React, {Component} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {list, reset} from '../../actions/workitem/list';
import Board from 'react-trello';
import ExternalLink from '../entity/ExternalLink';
import './List.css'
import {update} from '../../actions/workitem/update';
import Estimate from './Estimate';
import upperFirst from 'lodash/upperFirst';
import TeamSprintRemainingCapacity from '../programincrement/TeamSprintRemainingCapacity';

class List extends Component {
  static propTypes = {
    epic: PropTypes.string.isRequired,
    projects: PropTypes.array.isRequired,
    teams: PropTypes.array.isRequired,
    sprints: PropTypes.array.isRequired,
    projectSettings: PropTypes.array.isRequired,
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
    this.props.list(this.props.epic);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  onEstimateChange(workitem, event) {
    const value = event.target.value.length > 0 ? parseFloat(event.target.value) : null;
    const estimateFieldName = `estimate${upperFirst(event.target.name)}`;
    this.props.update(workitem, {[estimateFieldName]: value});
  }

  getBoardCards(project, team, sprint) {
    const workitems = this.props.retrieved[this.props.epic]['hydra:member']
      .filter(w => w.project == project && w.team == team && w.sprint == sprint);
    return workitems.map(workitem => {
      return {
        id: workitem['@id'],
        title: <ExternalLink entity={workitem}/>,
        label: <Estimate workitem={workitem} onChange={this.onEstimateChange.bind(this, workitem)}/>
      };
    });
  }

  getBoardData() {
    const data = {};
    const columns = [];
    const unsignedColumnWidth = 15;
    const unsignedTeam = {'@id': null, 'name': 'Unsigned'};
    const unsignedSprint = {'@id': null, 'name': 'Unsigned'};
    for (let settings of this.props.projectSettings) {
      let columnsPerRow = settings.sprints.length;
      let columnWith = (100 - unsignedColumnWidth) / columnsPerRow;
      let teamIds = new Set();
      for (let capacity of settings.capacity || []) {
        teamIds.add(capacity.team);
      }
      let teams = this.props.teams.filter(t => teamIds.has(t['@id']));
      teams = [unsignedTeam, ...teams];
      let sprints = this.props.sprints.filter(s => settings.sprints.indexOf(s['@id']) !== -1);
      sprints = sprints.sort((a, b) => {
        a = new Date(a.startDate);
        b = new Date(b.startDate);
        return a.getTime() - b.getTime();
      })
      sprints = [unsignedSprint, ...sprints];
      for (let team of teams) {
        for (let sprintIndex in sprints) {
          let sprint = sprints[sprintIndex];
          columns.push({
            id: [this.props.epic, settings.project, team['@id'], sprint['@id']].join(':'),
            title: `${team.name} ${'Unsigned' === sprint.name ? sprint.name : 'S' + sprintIndex}`,
            label: <TeamSprintRemainingCapacity epic={this.props.epic} team={team['@id']} sprint={sprint['@id']}
                                                projectSettings={settings}/>,
            cards: this.getBoardCards(settings.project, team['@id'], sprint['@id']),
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
    const workitem = this.props.retrieved[this.props.epic]['hydra:member']
      .find(w => w['@id'] === cardId);
    let patch = {
      project: targetProject,
      team: targetTeam ? targetTeam : null,
      sprint: targetSprint ? targetSprint : null
    };
    if (targetProject !== sourceProject) {
      patch.backlogGroup = this.props.projectSettings.find(s => s.project === targetProject).defaultBacklogGroup;
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

        {this.props.retrieved && this.props.retrieved[this.props.epic] && (
          <Board id={this.props.epic} data={this.getBoardData()} editable={true} laneDraggable={false}
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
    teams: state.team.list.retrieved['hydra:member'],
    sprints: state.sprint.list.retrieved['hydra:member'],
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
