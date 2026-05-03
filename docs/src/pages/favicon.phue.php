<layout.default title="favicon test" :page="page">
    <article>
        <div class="favicons">
            <div>
                <sft.favicon show square color="yellow"></sft.favicon>
            </div>
            <div>
                <sft.favicon show circle color="blue"></sft.favicon>
            </div>
            <div>
                <sft.favicon show circle background="black" color="red" size="50"></sft.favicon>
            </div>
            <div>
                <sft.favicon show circle background="black" color="green" size="20"></sft.favicon>
            </div>
            <div>
                <sft.favicon show square color="yellow">t</sft.favicon>
            </div>
            <div>
                <sft.favicon show circle color="blue">sf</sft.favicon>
            </div>

        </div>
    </article>
</layout.default>

<style global>
    div.favicons {
        display: flex;
        flex-wrap: auto;
        gap: 2rem;

        div {
            width: 32px;
            height: 32px;
        }
    }
</style>