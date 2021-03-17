select
stb.soccer_team_id,
st.slug,
st.acron,
sum((stb.win*3)+(stb.draw*1)) as pts,
sum((stb.win)+(stb.draw)+(stb.los)) as j,
sum(stb.win) as v,
sum(stb.draw) as e,
sum(stb.los) as d,
sum(stb.pro_goal) as gp,
sum(stb.own_goal) as gc,
sum((stb.pro_goal)-(stb.own_goal)) as sg,
format(round(sum((stb.win*3)+(stb.draw*1))/sum(((stb.win)+(stb.draw)+(stb.los))*3),5)*100,1) as ap,
format(100*(1/format(round(sum((stb.win*3)+(stb.draw*1))/sum(((stb.win)+(stb.draw)+(stb.los))*3),5)*100,1)),2) as odds
from soccer_table as stb
inner join soccer_team as st on st.id = stb.soccer_team_id
group by stb.soccer_team_id
order by pts desc