-- CREATE OR REPLACE VIEW `view_table_odd` AS
SELECT 
soccer_table.soccer_team_id,
soccer_team.slug,
soccer_team.acron,
sum((soccer_table.win*3)+(soccer_table.draw*1)) as pts,
sum((soccer_table.win)+(soccer_table.draw)+(soccer_table.los)) as j,
sum(soccer_table.win) as v,
sum(soccer_table.draw) as e,
sum(soccer_table.los) as d,
sum(soccer_table.pro_goal) as gp,
sum(soccer_table.own_goal) as gc,
sum((soccer_table.pro_goal)-(soccer_table.own_goal)) as sg,
format(round(sum((soccer_table.win*3)+(soccer_table.draw*1))/sum(((soccer_table.win)+(soccer_table.draw)+(soccer_table.los))*3),5)*100,1) as ap,
IFNULL(format(100*(1/format(round(sum((soccer_table.win*3)+(soccer_table.draw*1))/sum(((soccer_table.win)+(soccer_table.draw)+(soccer_table.los))*3),5)*100,1)),2), 2.32) as odds

FROM football_league
INNER JOIN soccer_match on soccer_match.football_league_id = football_league.id
INNER JOIN soccer_team
INNER JOIN soccer_table on soccer_table.soccer_team_id = soccer_team.id
WHERE football_league.status = 1
GROUP BY soccer_table.soccer_team_id
ORDER BY pts DESC