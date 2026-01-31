using AutoMapper;
using Microsoft.EntityFrameworkCore;
using student_list.Api.Data;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
// Learn more about configuring Swagger/OpenAPI at https://aka.ms/aspnetcore/swashbuckle
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

// Allow the Vue dev server to call the API
builder.Services.AddCors(options =>
{
    options.AddPolicy("FrontendDev", policy =>
        policy.WithOrigins("http://localhost:5173", "http://localhost:5174")
              .AllowAnyHeader()
              .AllowAnyMethod());
});

// Add DbContext
var connectionString = builder.Configuration.GetConnectionString("DefaultConnection");
builder.Services.AddDbContext<student_list.Api.Data.StudentDbContext>(options =>
    options.UseSqlServer(connectionString));

// Add AutoMapper from Application assembly
builder.Services.AddAutoMapper(typeof(student_list.Application.StudentProfile).Assembly);

var app = builder.Build();

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();

app.UseCors("FrontendDev");

//select all students
app.MapGet("/students", async (student_list.Api.Data.StudentDbContext db, IMapper mapper) =>
{
    var entities = await db.Students.AsNoTracking().ToListAsync();
    var models = mapper.Map<List<student_list.Domain.Models.StudentModel>>(entities);
    return Results.Ok(models);
});
app.MapGet("/students/count", async (StudentDbContext db) =>
    Results.Ok(await db.Students.CountAsync()));


// Minimal endpoint to create student
app.MapPost("/students", async (student_list.Domain.Models.StudentModel model, student_list.Api.Data.StudentDbContext db, IMapper mapper) =>
{
    var entity = mapper.Map<student_list.Domain.Entities.Student>(model);
    db.Students.Add(entity);
    await db.SaveChangesAsync();
    var created = mapper.Map<student_list.Domain.Models.StudentModel>(entity);
    return Results.Created($"/students/{created.Id}", created);
});

// update student by id
app.MapPut("/students/{id:int}", async (int id, student_list.Domain.Models.StudentModel model, student_list.Api.Data.StudentDbContext db, IMapper mapper) =>
{
    var entity = await db.Students.FindAsync(id);
    if (entity is null)
    {
        return Results.NotFound();
    }

    mapper.Map(model, entity);
    entity.Id = id;

    await db.SaveChangesAsync();
    var updated = mapper.Map<student_list.Domain.Models.StudentModel>(entity);
    return Results.Ok(updated);
});

// delete student by id
app.MapDelete("/students/{id:int}", async (int id, student_list.Api.Data.StudentDbContext db) =>
{
    var entity = await db.Students.FindAsync(id);
    if (entity is null)
    {
        return Results.NotFound();
    }

    db.Students.Remove(entity);
    await db.SaveChangesAsync();
    return Results.NoContent();
});



app.Run();
