import plotly.graph_objects as go
import pandas as pd
import numpy as np
import os
from Bio import SeqIO


shapes_colors = {
    'Purifying': ('#FF0000', 'circle'),
    'Positive': ('#0000FF', 'square')
}
underline_colors = ['teal', 'gray', 'firebrick', 'royalblue', 'turquoise', 'green', 'pink', 'orange', 'purple']
        
SEQ = list(SeqIO.parse("EC_Proteins.fasta", "fasta"))
SEQ = {
    record.id: str(record.seq) for record in SEQ
}
df1 = pd.read_csv("Evo_Sites.tsv", sep = '\t')
df2 = pd.read_csv("pfam_Interpro_EC.tsv", sep='\t')

for uid in SEQ.keys():
    sequence = SEQ[uid]
    print(uid, len(sequence))

    NPOS_PERLINE = 50
    H_SPACE = 2.5
    V_SPACE = 5
    
    # width = (NPOS_PERLINE + 13) * H_SPACE * 10
    height = (len(sequence) // NPOS_PERLINE + 1) * V_SPACE * 10
    
    fig = go.Figure()
    
    d1 = df1[df1['Protein'] == uid]
    g1 = d1.groupby('Selction')
    assert g1.ngroups <= 2, f"Error: {uid} has more than 2 groups in Selection"

    hover_texts_dct = {}
    g2 = d1.groupby('Site')
    for site, group in g2:
        hvr_text = f"Site: {site}"
        for sel in group['Selction'].unique():
            hvr_text += f"<br>{sel}: {group[group['Selction'] == sel]['Model'].values[0]}"
        hover_texts_dct[site] = hvr_text

    mod_pos = []
    for i, (name, group) in enumerate(g1):
        mod_positions = group['Site'].apply(lambda x: x - 1)

        mod_pos += mod_positions.tolist()
        diagram_positions_x, diagram_positions_y = [], []
        hover_texts = [hover_texts_dct.get(pos + 1, '') for pos in mod_positions]
        
        for idx, pos in enumerate(mod_positions):
            xpos = (pos % NPOS_PERLINE) * H_SPACE
            ypos = (pos // NPOS_PERLINE) * V_SPACE
            diagram_positions_x.append(xpos)
            diagram_positions_y.append(ypos)
        
        if len(diagram_positions_x) == 0:
            continue
            
        fig.add_trace(go.Scatter(
            x=diagram_positions_x,
            y=diagram_positions_y,
            mode='markers',
            marker=dict(
                color=shapes_colors[name][0],
                size=15,
                opacity=0.5,
                symbol=shapes_colors[name][1],
                line=dict(color='black', width=1)
            ),
            name=name,
            hovertemplate='%{text}<extra></extra>',
            text=hover_texts,
            hoverlabel=dict(
                bgcolor="white",
                bordercolor="black",
                font_size=12,
                namelength=-1
            ),
            showlegend=True
        ))

    for i, aa in enumerate(sequence):
        xpos = (i % NPOS_PERLINE) * H_SPACE
        ypos = (i // NPOS_PERLINE) * V_SPACE
        txt_color = 'black' if i in mod_pos else 'grey'
        
        fig.add_trace(go.Scatter(
            x=[xpos],
            y=[ypos],
            mode='text',
            text=[aa],
            textfont=dict(color=txt_color, size=12),
            showlegend=False,
            hoverinfo='skip'
        ))

    # ------------------------------------------------------------------------------------------------
    d2 = df2[df2['UniprotID'] == uid]
    uspace = np.array([1.2 for _ in range(len(sequence))])
    
    for k in range(d2.shape[0]):
        r = d2.iloc[k]
        sp, ep = r['From'] - 1, r['To'] - 1
        ys, ye = (sp // NPOS_PERLINE) * V_SPACE, (ep // NPOS_PERLINE) * V_SPACE
        us = max(uspace[sp:ep+1])
        
        line_x, line_y = [], []
        
        for yl in range(ys, ye + 1, V_SPACE):
            if yl == ys:
                xs = (sp % NPOS_PERLINE) * H_SPACE
            else:
                xs = 0
            if yl == ye:
                xe = (ep % NPOS_PERLINE) * H_SPACE
            else:
                xe = (NPOS_PERLINE - 1) * H_SPACE
            
            line_x.extend([xs, xe, None])
            line_y.extend([yl + us, yl + us, None])
        
        fig.add_trace(go.Scatter(
            x=line_x,
            y=line_y,
            mode='lines',
            line=dict(color=underline_colors[k], width=2),
            name=r['Short name'],
            showlegend=True,
            hoverinfo='skip'
        ))
        
        uspace[sp:ep+1] += 0.5

    # ------------------------------------------------------------------------------------------------
    fig.update_layout(
        title=dict(text=uid, x=0.5, font=dict(size=16)),
        autosize=True,
        width=None,
        height=height + 50,
        xaxis=dict(
            range=[-H_SPACE, (NPOS_PERLINE) * H_SPACE],
            showgrid=False,
            zeroline=False,
            showticklabels=False,
            domain=[0, 1]
        ),
        yaxis=dict(
            range=[(len(sequence) // NPOS_PERLINE + 1) * V_SPACE, -V_SPACE - (d2.shape[0] + 2)*3],
            showgrid=False,
            zeroline=False,
            showticklabels=False,
            domain=[0, 1]
        ),
        plot_bgcolor='white',
        paper_bgcolor='white',
        legend=dict(
            x=0.5,
            y=1,
            xanchor='center',
            yanchor='top',
            bgcolor='rgba(255,255,255,0.8)',
            bordercolor='black',
            borderwidth=2,
            itemsizing='constant',
            itemwidth=30
        ),
        margin=dict(l=0, r=0, t=10, b=0),
        hoverlabel=dict(
            bgcolor="white",
            bordercolor="black",
            font_size=12,
            align="left"
        ),
        hovermode='closest'
    )
    
    fig.write_html(f"{uid}.html")
    