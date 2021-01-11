import React from 'react';
import {makeStyles} from '@material-ui/core/styles';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import TeamFilter from './TeamFilter';
import TeamSprintCapacity from './TeamSprintCapacity';

const useStyles = makeStyles(theme => ({
  root: {
    position: 'sticky',
    top: 0,
    zIndex: theme.zIndex.speedDial,
    background: theme.palette.background.default,
    marginBottom: theme.spacing(1),
  },
  title: {
    fontSize: '2rem',
    lineHeight: '3.5rem',
  },
  capacityContainer: {
    flexDirection: 'column',
  },
}));

const Header = props => {
  const classes = useStyles(props);

  return (
    <div className={classes.root}>
      <ExpansionPanel defaultExpanded={true}>
        <ExpansionPanelSummary expandIcon={<ExpandMoreIcon/>}>
          <Typography variant="h1" className={classes.title}>{props.title}</Typography>
          <TeamFilter/>
        </ExpansionPanelSummary>
        <ExpansionPanelDetails classes={{root: classes.capacityContainer}}>
          <TeamSprintCapacity/>
        </ExpansionPanelDetails>
      </ExpansionPanel>
    </div>
  );
}

export default Header;
