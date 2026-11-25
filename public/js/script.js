/*
 * Deník postavy – interaktivita (jen na stránce detailu deníku):
 *  1) políčka Tělo/Duše/Vliv – aktivní jsou vždy zleva a souvisle, nad maximem jsou přeškrtnutá,
 *  2) sponky Ohrožení/Výhoda – tažení myší/prstem, přichycení k číslu, návrat při puštění mimo stupnici,
 *  3) povolání v režimu úprav – přidání ze seznamu, změna úrovně (− +) a odstranění (×) přes fetch.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('dennik-form');
    if (!form) {
        return;
    }
    initBoxes(form);
    form.querySelectorAll('.clip').forEach((clip) => initClip(form, clip));
    initPovolania(form);
});

/* ------------------------------------------------------------------ 1) políčka vlastností */
function initBoxes(form) {
    form.querySelectorAll('.attr .boxes').forEach((group) => {
        const boxes = [...group.querySelectorAll('input[type=checkbox]')];
        const max = Number(group.dataset.max) || 0;

        // zaškrtne prvních n políček (zleva), ostatní zruší; políčka nad maximem se nikdy nezaškrtnou
        const setValue = (n) => {
            const value = Math.max(0, Math.min(n, max));
            boxes.forEach((box, i) => {
                box.checked = i < value;
            });
        };

        boxes.forEach((box, i) => {
            box.addEventListener('click', (event) => {
                if (box.disabled) {
                    event.preventDefault();
                    return;
                }
                const active = boxes.filter((b) => b.checked).length;
                // klik na poslední aktivní políčko ho ubere, jinak se aktivní počet nastaví na pozici kliknutí
                setValue(active === i + 1 && box.checked ? i : i + 1);
            });
        });
        setValue(boxes.filter((b) => b.checked).length);
    });
}

/* ------------------------------------------------------------------ 2) sponky (posuvníky) */
function initClip(form, clip) {
    const page = clip.closest('.page');
    const scale = page.querySelector('.scale--' + clip.dataset.scale);
    const input = scale.querySelector('input[type=hidden]');
    const slots = [...scale.querySelectorAll('.scale__nums li')];   // pořadí 10..0 (shora dolů)
    const editMode = document.getElementById('edit-mode');

    let value = Number(input.value) || 0;
    let dragging = false;
    let offsetY = 0;

    const px = () => page.getBoundingClientRect().width / 760;
    const centerOf = (slot) => {
        const r = slot.getBoundingClientRect();
        return r.top + r.height / 2 - page.getBoundingClientRect().top;
    };
    const slotOf = (n) => slots.find((s) => Number(s.dataset.n) === n);

    // spona se vždy dotýká jen okraje listu: svisle se hýbe, vodorovně je pevná (nezajede hlouběji než skutečná)
    const moveTo = (centerY) => {
        clip.style.top = centerY - clip.offsetHeight / 2 + 'px';
    };
    const show = (n, animate) => {
        value = n;
        input.value = n;
        clip.setAttribute('aria-valuenow', n);
        clip.classList.toggle('is-snapping', animate);
        moveTo(centerOf(slotOf(n)));
    };

    // nejbližší číslo ke svislé poloze (v px vůči stránce)
    const nearest = (y) => slots.reduce((best, s) => (Math.abs(centerOf(s) - y) < Math.abs(centerOf(best) - y) ? s : best));
    const bounds = () => [centerOf(slots[0]), centerOf(slots[slots.length - 1])];

    clip.addEventListener('pointerdown', (event) => {
        if (!editMode.checked) {
            return;
        }
        dragging = true;
        clip.setPointerCapture(event.pointerId);
        clip.classList.add('is-dragging');
        clip.classList.remove('is-snapping');
        offsetY = event.clientY - clip.getBoundingClientRect().top - clip.offsetHeight / 2;
        event.preventDefault();
    });

    clip.addEventListener('pointermove', (event) => {
        if (!dragging) {
            return;
        }
        const [top, bottom] = bounds();
        const pageTop = page.getBoundingClientRect().top;
        const y = event.clientY - offsetY - pageTop;
        const pad = 12 * px();
        moveTo(Math.max(top - pad, Math.min(bottom + pad, y)));
    });

    const release = (event) => {
        if (!dragging) {
            return;
        }
        dragging = false;
        clip.classList.remove('is-dragging');
        const pageRect = page.getBoundingClientRect();
        const [top, bottom] = bounds();
        const y = event.clientY - pageRect.top;
        const half = slots[0].getBoundingClientRect().height / 2;
        const insideY = y >= top - half && y <= bottom + half;
        const insideX = event.clientX >= pageRect.left - 40 * px() && event.clientX <= pageRect.left + 90 * px();
        // pustíme-li mimo stupnici, vrátí se na původní číslo
        show(insideX && insideY ? Number(nearest(y).dataset.n) : value, true);
    };
    clip.addEventListener('pointerup', release);
    clip.addEventListener('pointercancel', () => {
        dragging = false;
        clip.classList.remove('is-dragging');
        show(value, true);
    });

    // klik na číslo posune sponu na toto číslo
    slots.forEach((slot) => slot.addEventListener('click', () => editMode.checked && show(Number(slot.dataset.n), true)));

    // ovládání klávesnicí: šipky nahoru/dolů, Home/End
    clip.addEventListener('keydown', (event) => {
        const step = { ArrowUp: 1, ArrowRight: 1, ArrowDown: -1, ArrowLeft: -1 }[event.key];
        if (!editMode.checked || (step === undefined && event.key !== 'Home' && event.key !== 'End')) {
            return;
        }
        event.preventDefault();
        const next = event.key === 'Home' ? 0 : event.key === 'End' ? 10 : value + step;
        show(Math.max(0, Math.min(10, next)), true);
    });

    // geometrie sponky se počítá z šířky listu v px (spona je vždy ukotvená k listu, ne k oknu prohlížeče)
    const place = () => {
        const u = page.clientWidth / 760;
        page.style.position = 'relative';
        clip.style.position = 'absolute';
        clip.style.width = 78 * u + 'px';
        clip.style.height = 26 * u + 'px';
        clip.style.left = -26 * u + 'px';
        show(value, false);
    };
    new ResizeObserver(place).observe(page);
    place();
}

/* ------------------------------------------------------------------ 3) povolání (přidat / změnit úroveň / odstranit) */
function initPovolania(form) {
    const box = document.getElementById('povolania-box');
    const cells = () => [...box.querySelectorAll('.class-cell--job')];
    // const abilitiesBox = form.querySelector('.abilities__list');
    let dostupne = JSON.parse(form.dataset.stav || '{}').dostupne || [];
    let povolania = JSON.parse(form.dataset.stav || '{}').povolania || [];

    const esc = (text) => String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const cellHtml = (p, index) => {
        const label = `aria-label="Povolání ${index + 1}"`;
        if (!p) {
            return `<input class="plain" type="text" readonly tabindex="-1" ${label} value="">`
                + '<button type="button" class="class-add" data-act="open-picker" aria-haspopup="listbox">+ Přidat povolání</button>'
                + '<div class="picker" hidden role="listbox" aria-label="Povolání, která postava ještě nemá">'
                + dostupne.map((d) => `<button type="button" role="option" data-act="add" data-id="${d.id}">${esc(d.nazov)}</button>`).join('')
                + (dostupne.length ? '' : '<p class="picker__empty">Žádné další povolání.</p>') + '</div>';
        }
        const levels = [1, 2, 3, 4, 5].map((i) => `<input type="checkbox" disabled${i <= p.uroven ? ' checked' : ''} aria-label="Úroveň ${i}">`).join('');
        return `<input class="plain" type="text" readonly tabindex="-1" ${label} value="${esc(p.nazov)}">`
            + `<span class="boxes boxes--narrow ro" role="group" aria-label="Úroveň povolání ${index + 1}">${levels}</span>`
            + `<span class="class-ctl" data-id="${p.id}">`
            + `<button type="button" data-act="minus" aria-label="Snížit úroveň"${p.uroven <= 1 ? ' disabled' : ''}>−</button>`
            + `<button type="button" data-act="plus" aria-label="Zvýšit úroveň"${p.uroven >= 5 ? ' disabled' : ''}>+</button>`
            + '<button type="button" data-act="remove" aria-label="Odstranit povolání">×</button></span>';
    };

    // řádek zvláštní schopnosti: získaná, nebo volné místo s tlačítkem a seznamem schopností daného povolání
    const abilityHtml = (a, i) => {
        if (!a || !a.pending) {
            return `<input class="plain" type="text" readonly tabindex="-1" aria-label="Zvláštní schopnost ${i + 1}" value="${esc(a ? a.text : '')}">`;
        }
        const options = (povolania.find((p) => p.id === a.idPovolanie) || { moznosti: [] }).moznosti;
        return `<button type="button" class="ability-add" data-act="open-ability" aria-haspopup="listbox">+ Přidat zvláštní schopnost – ${esc(a.text)}</button>`
            + `<div class="picker picker--abilities" hidden role="listbox" aria-label="Schopnosti povolání ${esc(a.text)}">`
            + options.map((m) => `<button type="button" role="option" data-act="pick-ability" data-povolanie="${a.idPovolanie}" data-id="${m.id}">${esc(m.nazov)}</button>`).join('')
            + (options.length ? '' : '<p class="picker__empty">Žádná schopnost není dostupná.</p>') + '</div>';
    };

    // dialog: při snížení úrovně si hráč vybere schopnost, která zanikne
    const askAbility = (volba) => new Promise((resolve) => {
        const dialog = document.createElement('dialog');
        dialog.className = 'ability-dialog';
        dialog.innerHTML = '<p>Snížením úrovně musí jedna zvláštní schopnost zaniknout. Kterou chcete zrušit?</p>'
            + volba.schopnosti.map((a) => `<button type="button" data-id="${a.id}">${esc(a.nazov)}</button>`).join('')
            + '<button type="button" class="ability-dialog__cancel">Zrušit</button>';
        dialog.addEventListener('click', (event) => {
            const btn = event.target.closest('button');
            if (btn) {
                dialog.close(btn.dataset.id || '');
            }
        });
        dialog.addEventListener('close', () => {
            resolve(dialog.returnValue || null);
            dialog.remove();
        });
        document.body.appendChild(dialog);
        dialog.showModal();
    });

    // překreslí povolání i seznam zvláštních schopností podle odpovědi serveru
    const render = (stav) => {
        dostupne = stav.dostupne;
        cells().forEach((cell, i) => {
            cell.innerHTML = cellHtml(stav.povolania[i] || null, i);
        });
        povolania = stav.povolania;
        form.querySelectorAll('.abilities__list li').forEach((li, i) => {
            li.innerHTML = abilityHtml(stav.schopnosti[i] || null, i);
        });
    };

    const send = async (url, data) => {
        try {
            const response = await fetch(url, { method: 'POST', body: new URLSearchParams(data), headers: { Accept: 'application/json' } });
            const json = await response.json();
            if (response.status === 409 && json.volba) {
                const zrusit = await askAbility(json.volba);
                return zrusit ? send(url, { ...data, idSchopnost: zrusit }) : undefined;
            }
            if (!response.ok) {
                throw new Error(json.chyba || 'Akci se nepodařilo provést.');
            }
            render(json);
        } catch (error) {
            alert(error.message);
        }
    };

    const closePickers = () => form.querySelectorAll('.picker').forEach((p) => { p.hidden = true; });

    form.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-act]');
        if (!button) {
            return;
        }
        const act = button.dataset.act;
        const id = button.dataset.id || (button.closest('.class-ctl') || {}).dataset?.id;

        if (act === 'open-picker') {
            const picker = button.parentElement.querySelector('.picker');
            const wasHidden = picker.hidden;
            closePickers();
            picker.hidden = !wasHidden;
        } else if (act === 'open-ability') {
            const picker = button.parentElement.querySelector('.picker');
            const wasHidden = picker.hidden;
            closePickers();
            picker.hidden = !wasHidden;
        } else if (act === 'pick-ability') {
            closePickers();
            send(form.dataset.urlAbility, { idPovolanie: button.dataset.povolanie, idSchopnost: id });
        } else if (act === 'add') {
            closePickers();
            send(form.dataset.urlAdd, { idPovolanie: id });
        } else if (act === 'plus' || act === 'minus') {
            send(form.dataset.urlLevel, { idPovolanie: id, smer: act === 'plus' ? 1 : -1 });
        } else if (act === 'remove' && confirm('Opravdu chcete odstranit toto povolání? Zmizí i jeho zvláštní schopnosti.')) {
            send(form.dataset.urlRemove, { idPovolanie: id });
        }
    });

    // seznam se zavře klikem mimo něj nebo klávesou Escape
    document.addEventListener('click', (event) => event.target.closest('.class-cell--job, .abilities__list li') || closePickers());
    document.addEventListener('keydown', (event) => event.key === 'Escape' && closePickers());
}
