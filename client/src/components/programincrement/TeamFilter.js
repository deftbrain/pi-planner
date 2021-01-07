import React from 'react';
import {connect} from 'react-redux';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import {makeStyles} from '@material-ui/core/styles';
import {setTeamFilter} from '../../actions/programincrement/show';

function stopPropagation(e) {
  e.stopPropagation();
}

const useStyles = makeStyles(theme => ({
  formControl: {
    margin: `${theme.spacing(1)}px ${theme.spacing(3)}px`,
    minWidth: 150,
  }
}));

const TeamFilter = props => {
  const classes = useStyles();

  if (!props.teams) {
    return null;
  }

  return (
    <FormControl className={classes.formControl} variant="outlined" margin='dense'>
      <Select labelId="team-filter-label" value={props.selectedTeam} onChange={props.onChange} onClick={stopPropagation}
              onFocus={stopPropagation} displayEmpty={true} inputProps={{'aria-label': 'Team filter'}}>
        <MenuItem value="">All teams</MenuItem>
        {props.teams['hydra:member'].map(team => (
          <MenuItem key={team['@id']} value={team['@id']}>{team.name}</MenuItem>
        ))}
      </Select>
    </FormControl>
  );
}

const mapStateToProps = state => ({
  teams: state.team.list.retrieved,
  selectedTeam: state.programincrement.show.teamFilter,
});
const mapDispatchToProps = dispatch => ({
  onChange: event => dispatch(setTeamFilter(event.target.value)),
});

export default connect(mapStateToProps, mapDispatchToProps)(TeamFilter);
