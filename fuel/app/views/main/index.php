<p>
    Šeit būs pārskats par saglabātajiem datiem sistēmā
</p>
<table>
    <tr>    
        <th>
            Articles
        </th>
    </tr>
    <tr>
        <td>
            Article count
        </td>
        <td>
            <?php echo $article_count; ?> 
        </td>
    </tr>
    <tr>
        <td>
            Articles parsed count
        </td>
        <td>
            <?php echo $article_parsed_count; ?> 
        </td>
    </tr>
    <tr>
        <td>
            Articles ready for parsing count
        </td>
        <td>
            <?php echo $article_count - $article_parsed_count; ?> 
        </td>
    </tr>
    
    <tr>    
        <th>
            Paragraphs
        </th>
    </tr>
    <tr>
        <td>
            Parsed paragraph count
        </td>
        <td>
            <?php echo $paragraph_count; ?> 
        </td>
    </tr>
    <tr>
        <td>
            Parsed paragraph word count
        </td>
        <td>
            <?php echo $word_count; ?> 
        </td>
    </tr>
    <tr>    
        <th>
            Blogs
        </th>
    </tr>
    <tr>
        <td>
            Blog count
        </td>
        <td>
            <?php echo $blog_count; ?> 
        </td>
    </tr>
</table>


