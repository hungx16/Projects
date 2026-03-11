using AutoMapper;
using System;
using System.Linq;
using student_list.Domain.Entities;
using student_list.Domain.Models;

namespace student_list.Application
{
    public class Application
    {

    }

    public class StudentProfile : Profile
    {
        public StudentProfile()
        {
            CreateMap<Student, StudentModel>()
                .ForMember(
                    dest => dest.FullName,
                    opt => opt.MapFrom(src =>
                        string.Join(" ", new[] { src.FirstName, src.LastName }
                            .Where(value => !string.IsNullOrWhiteSpace(value)))))
                .ForMember(
                    dest => dest.Age,
                    opt => opt.MapFrom(src =>
                        DateTime.Today.Year
                        - src.BirthDate.Year
                        - (DateTime.Today < src.BirthDate.Date.AddYears(DateTime.Today.Year - src.BirthDate.Year)
                            ? 1
                            : 0)))
                .ReverseMap();
        }
    }
}
